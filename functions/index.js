const functions = require('firebase-functions')
const admin = require('firebase-admin')

admin.initializeApp()

exports.ingest = functions.https.onRequest(async (request, response) => {
  if (request.body.key !== functions.config().ingest.key) {
    response.status(401).send('invalid key')
  }

  try {
    const updates = {}
    for (const [key, value] of Object.entries(request.body.data)) {
      updates[`${key}/updated_at`] = value.updated_at
      updates[`${key}/retweet_count`] = value.retweet_count
      updates[`${key}/favorite_count`] = value.favorite_count
    }
    await admin.database().ref('live/rooms').update(updates)
    response.send('OK!')
  } catch (error) {
    console.error(error)
    response.status(500).send('Error')
  }
})
