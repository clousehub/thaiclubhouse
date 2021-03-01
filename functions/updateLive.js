const admin = require('firebase-admin')
const throat = require('throat').default(8)
const getRoomInfo = require('./getRoomInfo')

async function updateLive() {
  const roomsRef = admin.database().ref('live/rooms')
  const plotKey = `time_${Date.now()}`

  // Check rooms with no status
  const uncheckedSnapshot = await roomsRef
    .orderByChild('status')
    .equalTo(null)
    .once('value')

  const liveSnapshot = await roomsRef
    .orderByChild('status')
    .equalTo('active')
    .once('value')

  const promises = []

  /**
   * @param {import('firebase-admin').database.DataSnapshot} snapshot
   */
  const checkRoom = (snapshot) => {
    const updatedAt = snapshot.child('stats_updated_at').val()
    if (!(!updatedAt || Date.now() > Date.parse(updatedAt) + 180e3)) {
      return
    }
    promises.push(
      throat(async () => {
        try {
          const roomId = snapshot.key
          const status = snapshot.child('status').val()
          const roomInfo = await getRoomInfo(roomId)
          const updates = {}
          updates['status'] = roomInfo.status
          if (!snapshot.child('stats_created_at').val()) {
            updates['stats_created_at'] = Date.now()
          }
          updates['stats_updated_at'] = Date.now()
          if (roomInfo.info) {
            updates['info/title'] = roomInfo.info.title
            updates['info/description'] = roomInfo.info.description
            updates['info/speakers'] = roomInfo.info.speakers
            updates['info/participants'] = roomInfo.info.participants
            updates[`stats/speakers/${plotKey}`] = roomInfo.info.speakers
            updates[`stats/participants/${plotKey}`] =
              roomInfo.info.participants
          }
          await snapshot.ref.update(updates)
          console.log(
            '%s: %s -> %s',
            roomId,
            status || '(new)',
            updates['status']
          )
        } catch (error) {
          console.error(error)
        }
      })
    )
  }

  uncheckedSnapshot.forEach(checkRoom)
  liveSnapshot.forEach(checkRoom)
  await Promise.all(promises)
  console.log('done')
}

if (require.main === module) {
  admin.initializeApp({
    databaseURL:
      'https://thaiclubhouse-default-rtdb.europe-west1.firebasedatabase.app',
  })
  updateLive()
}

module.exports = updateLive
