import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v3.0.0-rc.17/dist/cookieconsent.umd.js';

// Enable dark mode
document.documentElement.classList.add('cc--darkmode');
CookieConsent.run({
    onConsent: function(){
        console.log('HELLO')
        if(CookieConsent.acceptedCategory('analytics')){
            // Analytics category enabled
        }

        if(CookieConsent.acceptedService('Google Analytics', 'analytics')){
            // Google Analytics enabled
        }
    },
    guiOptions: {
        consentModal: {
            layout: "box", //bar inline, box, cloud inline
            position: "bottom rigth", //bottom rigth, bottom left
            equalWeightButtons: true,
            flipButtons: true
        },
        preferencesModal: {
            layout: "bar wide",
            position: "right",
            equalWeightButtons: true,
            flipButtons: true
        }
    },
    categories: {
        necessary: {
            readOnly: true
        },
        analytics: {},
        marketing: {}
    },
    language: {
        default: "fr",
        autoDetect: "browser",
        translations: {
            fr: {
                consentModal: {
                    title: "Bonjour voyageur, c'est l'heure des cookies!",
                    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.",
                    acceptAllBtn: "Accepter tout",
                    acceptNecessaryBtn: "Tout rejeter",
                    showPreferencesBtn: "Gérer les préférences",
                    footer: "<a href=\"#link\">Politique de confidentialité</a>\n<a href=\"#link\">Termes et conditions</a>",
                    closeIconLabel: "Tout rejeter et fermer"
                },
                preferencesModal: {
                    title: "Préférences de cookies",
                    acceptAllBtn: "Accepter tout",
                    acceptNecessaryBtn: "Tout rejeter",
                    savePreferencesBtn: "Sauver les préférences",
                    closeIconLabel: "Fermer la modale",
                    serviceCounterLabel: "Services",
                    sections: [
                        {
                            title: "Utilisation de Cookies",
                            description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
                        },
                        {
                            title: "Cookies Strictement Nécessaires <span class=\"pm__badge\">Toujours Activé</span>",
                            description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                            linkedCategory: "necessary"
                        },
                        {
                            title: "Cookies Analytiques",
                            description: "Le service veut juste savoir combien de personnes consulte son site.",
                            linkedCategory: "analytics"
                        },
                        // {
                        //     title: "Cookies Publicitaires",
                        //     description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                        //     linkedCategory: "marketing"
                        // },
                        {
                            title: "Plus d'informations",
                            description: "Pour une question dans votre choix des cookies,  <a class=\"cc__link\" href=\"https://www.refaitesvosjeux.fr/contact\">contactez le service</a>."
                        }
                    ]
                }
            }
        }
    },
    disablePageInteraction: false
});