const functions = require('firebase-functions')
const admin = require('firebase-admin')

exports.ingest = functions.https.onRequest((request, response) => {
  // functions.logger.info('Hello logs!', {
  //   structuredData: true,
  // })
  response.send('Hello from Firebase!')
})
