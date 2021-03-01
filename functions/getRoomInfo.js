const got = require('got')
const metascraper = require('metascraper')([
  require('metascraper-description')(),
  require('metascraper-title')(),
])
const cheerio = require('cheerio')

async function getRoomInfo(roomId) {
  const { body, url } = await got(
    'https://www.joinclubhouse.com/room/' + roomId
  )
  const metadata = await metascraper({ html: body, url })
  if (metadata.description.startsWith('With')) {
    const $ = cheerio.load(body)
    const text = $('img[src*="icon_speech"]').parent().parent().text()
    const m = text && text.trim().match(/^(\d+)\s*\/\s*(\d+)$/)
    if (m) {
      return {
        status: 'active',
        info: {
          title: metadata.title,
          description: metadata.description,
          speakers: +m[2],
          participants: +m[1],
        },
      }
    }
  }
  return { status: 'inactive' }
}

if (require.main === module) {
  getRoomInfo(process.argv[2]).then(console.log)
}

module.exports = getRoomInfo
