const { exec } = require('child_process')

module.exports.exec = (command) => {
  const process = exec(command)

  process.stdout.on('data', (data) => {
    //console.log('stdout: ' + data.toString())
    console.log(data.toString())
  })

  process.stderr.on('data', (data) => {
    //console.log('stderr: ' + data.toString())
    console.log(data.toString())
  })

  process.on('exit', (code) => {  //this part fix of large string responses
    output= ""
    if(code){
      var output = code
    }
    console.log('child process exited with code ' + output.toString())
  })
}
