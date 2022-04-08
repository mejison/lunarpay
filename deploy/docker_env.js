const { exec } = require('./utilities.js')
const dataPromise = require('./config');

dataPromise.configPromise().then((config)=>{


var ENVIRONMENT='dev'
var PROJECT = config.PROJECT
var GIT_BRANCH = config.GIT_BRANCH
//var GIT_COMMIT_SHA = config.GIT_COMMIT_SHA
var CONTAINER_REGISTRY = config.CONTAINER_REGISTRY

if( GIT_BRANCH ) {
    GIT_BRANCH=GIT_BRANCH
}else{
    GIT_BRANCH=ENVIRONMENT
}

var command_env = `${CONTAINER_REGISTRY}${PROJECT}:${GIT_BRANCH}`
console.log (`${command_env}`)

})



