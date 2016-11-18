//# sourceMappingURL=http://example.com/path/to/your/sourcemap.map

window.App = new NamespaceApplication({
    debug: true,
    name: 'ownCollab Dashboard',
    url: OC.generateUrl('/apps/owncollab'),
    urlScript: '/apps/owncollab/js/',
    host: OC.getHost(),
    locale: OC.getLocale(),
    protocol: OC.getProtocol(),
    isAdmin: null,
    corpotoken: null,
    requesttoken: oc_requesttoken ? encodeURIComponent(oc_requesttoken) : null,
    uid: oc_current_user ? encodeURIComponent(oc_current_user) : null,
    constructsType: false
});

App.require('libs',
    [
        App.urlScript + 'libs/util.js'
    ],
    initLibrary, initError);

App.require('dependence',
    [
        // App Extensions
        App.urlScript + 'application/extension/dom.js',
        App.urlScript + 'application/extension/linker.js',

        // Modules
        // ...

        // Actions
        App.urlScript + 'application/action/api.js',
        App.urlScript + 'application/action/error.js',


        // Controllers
        App.urlScript + 'application/controller/page.js'

    ],
    initDependence, initError);

function initError(error){
    console.error('onRequireError' , error);
}

// start loading resources 'libs'
App.requireStart('libs');

function initLibrary(list){
    App.requireStart('dependence');
}

function initDependence(list){
    console.log('OwnCollab DashBoard Application start!');

    App.Controller.Page.construct();
}



