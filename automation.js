require('make-promises-safe')
require('dotenv').config()

const yargs = require('yargs')

const metascraper = require('metascraper')([
  require('metascraper-description')(),
  require('metascraper-title')(),
])
const fs = require('fs')
const crypto = require('crypto')
const got = require('got').default
const store = JSON.parse(fs.readFileSync('data/store.json', 'utf8'))

async function get(url) {
  const hash = crypto.createHash('md5').update(url).digest('hex')
  const path = `cache/${hash}`
  if (fs.existsSync(path)) {
    const data = JSON.parse(fs.readFileSync(path, 'utf8'))
    return data
  } else {
    const result = await got(url)
    const data = { body: result.body, url: result.url }
    fs.writeFileSync(path, JSON.stringify(data))
    return data
  }
}

function save() {
  fs.writeFileSync('data/store.json', JSON.stringify(store, null, 2), 'utf8')
}

const fromAmPm = (n, pm) => {
  if (n == 12 && pm) return 12
  if (n == 12 && !pm) return 0
  return n + (pm ? 12 : 0)
}

const search = (
  x,
  src,
  posted
) => {
  if (!x) return
  for (const m of x.matchAll(
    /(joinclubhouse\.com|clublink\.to)(?:\/|%2F)event(?:\/|%2F)(\w+)/g
  )) {
    const id = m[2]
    const event = (store.events[id] ??= {})
    event.sources ??= {}
    event.sources[src] ??= {}
    event.sources[src].created_time ??= posted
  }
  for (const m of x.matchAll(
    /(joinclubhouse\.com|clublink\.to)(?:\/|%2F)room(?:\/|%2F)(\w+)/g
  )) {
    const id = m[2]
    const room = (store.rooms[id] ??= {})
    room.sources ??= {}
    room.sources[src] ??= {}
    room.sources[src].created_time ??= posted
  }
}

yargs
  .strict()
  .demandCommand()
  .command('update-links-from-facebook', '', {}, async () => {
    const fetchPosts = async (url, page = 1) => {
      console.log('Fetching page', page)
      const { body } = await got(url, {
        responseType: 'json',
      }).catch(e => {
        const body = e.response?.body
        console.error('Error:', body)
        throw e
      })

      for (const item of body.data) {
        console.log(item.updated_time)
        search(item.message, item.permalink_url, item.created_time)
        for (const a of item.attachments?.data ?? []) {
          search(a.unshimmed_url, item.permalink_url, item.created_time)
          // search(a.description)
        }
        for (const a of item.comments?.data ?? []) {
          search(a.message, a.permalink_url, a.created_time)
        }
      }
      save()
      const nextPage = body.paging?.next
      if (nextPage && page < 2) {
        await fetchPosts(nextPage, page + 1)
      }
    }
    await fetchPosts(
      'https://graph.facebook.com/v9.0/430909721296190/feed' +
        '?fields=id%2Cmessage%2Ccreated_time%2Cupdated_time%2Cmessage_tags%2Cdescription%2Cname%2Cvia%2Cpermalink_url%2Cattachments%7Btype%2Ctitle%2Curl%2Cunshimmed_url%7D%2Ccomments.limit(10).order(reverse_chronological)%7Bmessage%2Cpermalink_url%7D' +
        '&limit=100' +
        '&access_token=' +
        process.env.FB_ACCESS_TOKEN
    )
  })
  .command('update-links-from-google-forms', '', {}, async () => {
    const url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQXSyLtc98_kQzPeyxl3vqZ_XBti_RIZdnqlf7TagGK6HOaRyDeMyi-uOPDmVJ9bk4E9l8a_9OoApGE/pub?gid=1287866987&single=true&output=csv'
    const { body: csv } = await got(url).catch(e => {
      const body = e.response?.body
      console.error('Error:', body)
      throw e
    })
    const records = require('csv-parse/lib/sync')(csv, {
      skip_empty_lines: true,
    })
    for (const [timestamp, record] of records) {
      const m = timestamp.match(/\d+/g)
      if (!m) continue
      const date = new Date(Date.UTC(
        +m[2],
        +m[0] - 1,
        +m[1],
        +m[3],
        +m[4],
        +m[5]
      ) - 3600e3 * 7).toJSON()
      search(record, 'https://thaiclubhouse.web.app/submit.html', date)
    }
    save()
  })
  .command('update-events', '', {}, async () => {
    for (const [id, event] of Object.entries(store.events)) {
      if (event.metadataUpdated) {
        continue
      }
      console.log(id)
      const { body, url } = await get(
        'https://www.joinclubhouse.com/event/' + id
      )
      const metadata = await metascraper({ html: body, url })
      if (metadata.description.includes(' with ')) {
        event.metadata = metadata
      }
      event.metadataUpdated ??= new Date().toJSON()
    }
    save()
  })
  .command('parse-events', '', {}, async () => {
    for (const [id, event] of Object.entries(store.events)) {
      if (event.metadata) {
        const description = event.metadata.description
        const date = parseDateFromDescription(description)
        if (date) {
          event.date = date
        } else {
          console.log('UNPARSED', description)
        }
      }
    }
    save()
  })
  .command('generate-store-commit-message', '', {}, () => {
    console.log('Update data state (data size: ' + (fs.statSync('data/store.json').size / 1024).toFixed(1) + ' KB)')
  })
  .parse()

function parseDateFromDescription(description) {
  const words = description.split(' ')
  const tz = {
    '+07': 7 * 3600e3,
    WIB: 7 * 3600e3,
    JST: 9 * 3600e3,
    WET: 0,
    CET: 1 * 3600e3,
    EST: -5 * 3600e3,
    MST: -7 * 3600e3,
    PST: -8 * 3600e3,
    AEST: 10 * 3600e3,
  }
  const tzmatch = tz[words[5]]
  if (words[3] === 'at' && tzmatch !== undefined) {
    const time =
      Date.UTC(
        2021,
        [
          'January',
          'February',
          'March',
          'April',
          'May',
          'June',
          'July',
          'August',
          'September',
          'October',
          'November',
          'December',
        ].indexOf(words[1]),
        +words[2],
        fromAmPm(parseInt(words[4].split(':')[0], 10), words[4].includes('pm')),
        parseInt(words[4].split(':')[1], 10)
      ) - tzmatch

    return new Date(time + 7 * 3600e3).toISOString().replace('Z', '+07:00')
  }
}
