const eggIncApi = require('./egg-inc-api/egginc_api.js')

require('yargs')
    .scriptName('Egg Inc API')
    .usage('$0 <cmd> [args]')
    .command('getAllActiveContracts', 'Get All Active Contracts', (yargs) => {}, (argv) => {
        eggIncApi.getContractAll().then((contracts) => {
            console.log(JSON.stringify(contracts))
        })
    })
    .command('getCoopStatus', 'Get Coop Status', (yargs) => {
        yargs
            .positional('contract', {type: 'string'})
            .positional('coop', {type: 'string'})
    }, (argv) => {
        eggIncApi.getContract(argv.contract, argv.coop).then((coopInfo) => {
            console.log(JSON.stringify(coopInfo))
        })
    })
    .help()
    .argv
