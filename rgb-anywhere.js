// rgb-anywhere.js
// used for putting party mode on stuff
// TODO: supposed to allow any given document to be RGB party'ed
const body = document.querySelector('body');

innerArray = [255, 0, 0];
i = 0
j = 1
partyActive =0

function cycleRGB() {
  if (innerArray[i] * innerArray[j] < 0) {
    console.log(innerArray[i], innerArray[j])
    i = j
    if (j == 2) {
      j = 0
    }
    else j ++
  }
  innerArray[i] -= 1
  innerArray[j] += 1
  body.style.backgroundColor = 'rgb(' + innerArray.join(',') + ')';
}

function partyMode () {
  partyActive = 1 - partyActive
  console.log(partyActive)
  if (partyActive > 0) {
    console.log('entered')
    //partyValues = [900000, 0, 0]
    //body.style.backgroundColor = '#' + partyValues[0]
    partyLength = setInterval(cycleRGB, 20)
  }
  else {
    body.style.backgroundColor = 'black'
    clearInterval(partyLength)
  }
}

partyMode()
