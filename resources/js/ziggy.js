    var Ziggy = {
        namedRoutes: {"home":{"uri":"\/","methods":["GET","HEAD"],"domain":null},"current-contracts":{"uri":"current-contracts","methods":["GET","HEAD"],"domain":null}},
        baseUrl: 'http://localhost:8000/',
        baseProtocol: 'http',
        baseDomain: 'localhost',
        basePort: 8000,
        defaultParameters: []
    };

    if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }
