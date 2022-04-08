const { exec } = require('./utilities.js')
const dataPromise = require('./config');

dataPromise.configPromise().then((config)=>{
    var GIT_BRANCH = config.GIT_BRANCH
    var GIT_COMMIT_SHA = config.GIT_COMMIT_SHA
    var DOCKER_REGISTRY_PATH = config.DOCKER_REGISTRY_PATH
    var command = `DOCKER_BUILDKIT=1 docker build . --tag ${DOCKER_REGISTRY_PATH}:${GIT_BRANCH}_${GIT_COMMIT_SHA}`  
    console.log(command)
    exec(command)
})
