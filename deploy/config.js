const { exec } = require("child_process")

var GIT_BRANCH = `git branch --show-current 2> /dev/null`.trim()
var GIT_COMMIT_SHA = `git rev-parse --verify HEAD 2> /dev/null`.trim()
const DOCKER_REGISTRY_PATH = process.env.DOCKER_REGISTRY_PATH 

function execPromise(command) {
  return new Promise(function(resolve, reject) {
    exec(command, (error, stdout, stderr) => {
      if (error) { reject(error)
        return
      }
      resolve(stdout.trim())
    })
  })
}

async function configPromise() {
  try {
    const data = {}
    data.GIT_BRANCH = await execPromise(GIT_BRANCH)
    data.GIT_COMMIT_SHA = await execPromise(GIT_COMMIT_SHA)
    data.DOCKER_REGISTRY_PATH = DOCKER_REGISTRY_PATH    
    return data  
  } catch (e) {
    console.error(e.message)
  }
}
exports.configPromise = configPromise
