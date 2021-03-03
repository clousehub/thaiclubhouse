import {
  html,
  render,
} from 'https://cdn.jsdelivr.net/npm/htm@3.0.4/preact/standalone.module.js'

let _contentsElement
function getContentsElement() {
  if (!_contentsElement) {
    _contentsElement = document.querySelector('#now_contents')
    if (_contentsElement) {
      _contentsElement.innerHTML = ''
    }
  }
  return _contentsElement
}

if (document.querySelector('#now_contents')) {
  firebase
    .database()
    .ref('live/rooms')
    .orderByChild('status')
    .equalTo('active')
    .on('value', (snapshot) => {
      render(
        html`<${LiveTable} data=${snapshot.val()} />`,
        getContentsElement()
      )
    })
}

function LiveTable({ data }) {
  console.log(data)
  const entries = Object.entries(data || {})
    .map(([id, room]) => ({ id, room }))
    .sort((a, b) => b.room.info.participants - a.room.info.participants)
  return html`
    <table>
      <col width="64" />
      <col width="100%" />
      <thead>
        <th style="text-align: right">👤 ${String.fromCharCode(160)}</th>
        <th>Topic</th>
      </thead>
      <tbody>
        ${entries.map(
          (entry) => html`<tr key=${entry.id}>
            <td style="text-align: right">
              ${entry.room.info.participants} ${String.fromCharCode(160)}
            </td>
            <td>
              <strong>
                <a href=${'https://www.joinclubhouse.com/room/' + entry.id}>
                  ${entry.room.info.title}
                </a>
              </strong>
              <!--
              <div
                style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
              >
                ${entry.room.info.description.replace(/^With/, 'with')}
              </div>
              -->
            </td>
          </tr>`
        )}
      </tbody>
    </table>
  `
}
