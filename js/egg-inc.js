const eggIncApi = require('./egg-inc-api/egginc_api.js')

require('yargs')
    .scriptName('Egg Inc API')
    .usage('$0 <cmd> [args]')
    .command('getAllActiveContracts', 'Get All Active Contracts', (yargs) => {}, (argv) => {
        eggIncApi.getContractAll().then((contracts) => {
            console.log(JSON.stringify(contracts))
        })
    })
    .help()
    .argv
