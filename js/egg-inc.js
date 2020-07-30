const eggIncApi = require('./egg-inc-api/egginc_api.js')

eggIncApi.getContractAll().then((contracts) => {
    console.log(JSON.stringify(contracts))
})
