var locationRating = document.querySelector('.js-locations');

var locations = locationRating.dataset.locations;

var simplemaps_worldmap_mapdata={
  main_settings: {
   //General settings
    width: "responsive", //'700' or 'responsive'
    background_color: "#FFFFFF",
    background_transparent: "yes",
    border_color: "#ffffff",
    popups: "detect",
    
    //State defaults
    state_description: "",
    state_color: "#88A4BC",
    state_hover_color: "#3B729F",
    state_url: "",
    border_size: 1.5,
    all_states_inactive: "no",
    all_states_zoomable: "no",
    
    //Location defaults
    location_description: "",
    location_color: "#FF0067",
    location_opacity: 0.8,
    location_hover_opacity: 1,
    location_url: "",
    location_size: 10,
    location_type: "square",
    location_image_source: "frog.png",
    location_border_color: "#FFFFFF",
    location_border: 2,
    location_hover_border: 2.5,
    all_locations_inactive: "no",
    all_locations_hidden: "no",
    
    //Label defaults
    label_color: "#d5ddec",
    label_hover_color: "#d5ddec",
    label_size: 22,
    label_font: "Arial",
    hide_labels: "no",
   
    //Zoom settings
    zoom: "yes",
    manual_zoom: "yes",
    back_image: "no",
    initial_back: "no",
    initial_zoom: "0",
    initial_zoom_solo: "yes",
    region_opacity: 1,
    region_hover_opacity: 0.6,
    zoom_out_incrementally: "yes",
    zoom_percentage: 0.99,
    zoom_time: 0.5,
    
    //Popup settings
    popup_color: "white",
    popup_opacity: 0.9,
    popup_shadow: 1,
    popup_corners: 5,
    popup_font: "12px/1.5 Verdana, Arial, Helvetica, sans-serif",
    popup_nocss: "no",
    
    //Advanced settings
    div: "map_rvj",
    auto_load: "yes",
    url_new_tab: "yes",
    images_directory: "default",
    fade_time: 0.1,
    link_text: "View Website",
    state_image_url: "",
    state_image_position: "",
    location_image_url: ""
  },
  state_specific: {
    AF: {
      name: "Afghanistan"
    },
    AO: {
      name: "Angola"
    },
    AL: {
      name: "Albania"
    },
    AE: {
      name: "United Arab Emirates"
    },
    AR: {
      name: "Argentina"
    },
    AM: {
      name: "Armenia"
    },
    AU: {
      name: "Australia"
    },
    AT: {
      name: "Austria"
    },
    AZ: {
      name: "Azerbaijan"
    },
    BI: {
      name: "Burundi"
    },
    BE: {
      name: "Belgique"
    },
    BJ: {
      name: "Benin"
    },
    BF: {
      name: "Burkina Faso"
    },
    BD: {
      name: "Bangladesh"
    },
    BG: {
      name: "Bulgaria"
    },
    BH: {
      name: "Bahrain"
    },
    BA: {
      name: "Bosnia and Herzegovina"
    },
    BY: {
      name: "Belarus"
    },
    BZ: {
      name: "Belize"
    },
    BO: {
      name: "Bolivia"
    },
    BR: {
      name: "Brazil"
    },
    BN: {
      name: "Brunei Darussalam"
    },
    BT: {
      name: "Bhutan"
    },
    BW: {
      name: "Botswana"
    },
    CF: {
      name: "Central African Republic"
    },
    CA: {
      name: "Canada"
    },
    CH: {
      name: "Switzerland"
    },
    CL: {
      name: "Chile"
    },
    CN: {
      name: "China"
    },
    CI: {
      name: "Côte d'Ivoire"
    },
    CM: {
      name: "Cameroon"
    },
    CD: {
      name: "Democratic Republic of the Congo"
    },
    CG: {
      name: "Republic of Congo"
    },
    CO: {
      name: "Colombia"
    },
    CR: {
      name: "Costa Rica"
    },
    CU: {
      name: "Cuba"
    },
    CZ: {
      name: "Czech Republic"
    },
    DE: {
      name: "Germany"
    },
    DJ: {
      name: "Djibouti"
    },
    DK: {
      name: "Denmark"
    },
    DO: {
      name: "Dominican Republic"
    },
    DZ: {
      name: "Algeria"
    },
    EC: {
      name: "Ecuador"
    },
    EG: {
      name: "Egypt"
    },
    ER: {
      name: "Eritrea"
    },
    EE: {
      name: "Estonia"
    },
    ET: {
      name: "Ethiopia"
    },
    FI: {
      name: "Finland"
    },
    FJ: {
      name: "Fiji"
    },
    GA: {
      name: "Gabon"
    },
    GB: {
      name: "United Kingdom"
    },
    GE: {
      name: "Georgia"
    },
    GH: {
      name: "Ghana"
    },
    GN: {
      name: "Guinea"
    },
    GM: {
      name: "The Gambia"
    },
    GW: {
      name: "Guinea-Bissau"
    },
    GQ: {
      name: "Equatorial Guinea"
    },
    GR: {
      name: "Greece"
    },
    GL: {
      name: "Greenland"
    },
    GT: {
      name: "Guatemala"
    },
    GY: {
      name: "Guyana"
    },
    HN: {
      name: "Honduras"
    },
    HR: {
      name: "Croatia"
    },
    HT: {
      name: "Haiti"
    },
    HU: {
      name: "Hungary"
    },
    ID: {
      name: "Indonesia"
    },
    IN: {
      name: "India"
    },
    IE: {
      name: "Ireland"
    },
    IR: {
      name: "Iran"
    },
    IQ: {
      name: "Iraq"
    },
    IS: {
      name: "Iceland"
    },
    IL: {
      name: "Israel"
    },
    IT: {
      name: "Italy"
    },
    JM: {
      name: "Jamaica"
    },
    JO: {
      name: "Jordan"
    },
    JP: {
      name: "Japan"
    },
    KZ: {
      name: "Kazakhstan"
    },
    KE: {
      name: "Kenya"
    },
    KG: {
      name: "Kyrgyzstan"
    },
    KH: {
      name: "Cambodia"
    },
    KR: {
      name: "Republic of Korea"
    },
    XK: {
      name: "Kosovo"
    },
    KW: {
      name: "Kuwait"
    },
    LA: {
      name: "Lao PDR"
    },
    LB: {
      name: "Lebanon"
    },
    LR: {
      name: "Liberia"
    },
    LY: {
      name: "Libya"
    },
    LK: {
      name: "Sri Lanka"
    },
    LS: {
      name: "Lesotho"
    },
    LT: {
      name: "Lithuania"
    },
    LU: {
      name: "Luxembourg"
    },
    LV: {
      name: "Latvia"
    },
    MA: {
      name: "Morocco"
    },
    MD: {
      name: "Moldova"
    },
    MG: {
      name: "Madagascar"
    },
    MX: {
      name: "Mexico"
    },
    MK: {
      name: "Macedonia"
    },
    ML: {
      name: "Mali"
    },
    MM: {
      name: "Myanmar"
    },
    ME: {
      name: "Montenegro"
    },
    MN: {
      name: "Mongolia"
    },
    MZ: {
      name: "Mozambique"
    },
    MR: {
      name: "Mauritania"
    },
    MW: {
      name: "Malawi"
    },
    MY: {
      name: "Malaysia"
    },
    NA: {
      name: "Namibia"
    },
    NE: {
      name: "Niger"
    },
    NG: {
      name: "Nigeria"
    },
    NI: {
      name: "Nicaragua"
    },
    NL: {
      name: "Netherlands"
    },
    NO: {
      name: "Norway"
    },
    NP: {
      name: "Nepal"
    },
    NZ: {
      name: "New Zealand"
    },
    OM: {
      name: "Oman"
    },
    PK: {
      name: "Pakistan"
    },
    PA: {
      name: "Panama"
    },
    PE: {
      name: "Peru"
    },
    PH: {
      name: "Philippines"
    },
    PG: {
      name: "Papua New Guinea"
    },
    PL: {
      name: "Poland"
    },
    KP: {
      name: "Dem. Rep. Korea"
    },
    PT: {
      name: "Portugal"
    },
    PY: {
      name: "Paraguay"
    },
    PS: {
      name: "Palestine"
    },
    QA: {
      name: "Qatar"
    },
    RO: {
      name: "Romania"
    },
    RU: {
      name: "Russia"
    },
    RW: {
      name: "Rwanda"
    },
    EH: {
      name: "Western Sahara"
    },
    SA: {
      name: "Saudi Arabia"
    },
    SD: {
      name: "Sudan"
    },
    SS: {
      name: "South Sudan"
    },
    SN: {
      name: "Senegal"
    },
    SL: {
      name: "Sierra Leone"
    },
    SV: {
      name: "El Salvador"
    },
    RS: {
      name: "Serbia"
    },
    SR: {
      name: "Suriname"
    },
    SK: {
      name: "Slovakia"
    },
    SI: {
      name: "Slovenia"
    },
    SE: {
      name: "Sweden"
    },
    SZ: {
      name: "Swaziland"
    },
    SY: {
      name: "Syria"
    },
    TD: {
      name: "Chad"
    },
    TG: {
      name: "Togo"
    },
    TH: {
      name: "Thailand"
    },
    TJ: {
      name: "Tajikistan"
    },
    TM: {
      name: "Turkmenistan"
    },
    TL: {
      name: "Timor-Leste"
    },
    TN: {
      name: "Tunisia"
    },
    TR: {
      name: "Turkey"
    },
    TW: {
      name: "Taiwan"
    },
    TZ: {
      name: "Tanzania"
    },
    UG: {
      name: "Uganda"
    },
    UA: {
      name: "Ukraine"
    },
    UY: {
      name: "Uruguay"
    },
    US: {
      name: "United States"
    },
    UZ: {
      name: "Uzbekistan"
    },
    VE: {
      name: "Venezuela"
    },
    VN: {
      name: "Vietnam"
    },
    VU: {
      name: "Vanuatu"
    },
    YE: {
      name: "Yemen"
    },
    ZA: {
      name: "South Africa"
    },
    ZM: {
      name: "Zambia"
    },
    ZW: {
      name: "Zimbabwe"
    },
    SO: {
      name: "Somalia"
    },
    GF: {
      name: "France"
    },
    FR: {
      name: "France"
    },
    ES: {
      name: "Spain"
    },
    AW: {
      name: "Aruba"
    },
    AI: {
      name: "Anguilla"
    },
    AD: {
      name: "Andorra"
    },
    AG: {
      name: "Antigua and Barbuda"
    },
    BS: {
      name: "Bahamas"
    },
    BM: {
      name: "Bermuda"
    },
    BB: {
      name: "Barbados"
    },
    KM: {
      name: "Comoros"
    },
    CV: {
      name: "Cape Verde"
    },
    KY: {
      name: "Cayman Islands"
    },
    DM: {
      name: "Dominica"
    },
    FK: {
      name: "Falkland Islands"
    },
    FO: {
      name: "Faeroe Islands"
    },
    GD: {
      name: "Grenada"
    },
    HK: {
      name: "Hong Kong"
    },
    KN: {
      name: "Saint Kitts and Nevis"
    },
    LC: {
      name: "Saint Lucia"
    },
    LI: {
      name: "Liechtenstein"
    },
    MF: {
      name: "Saint Martin (French)"
    },
    MV: {
      name: "Maldives"
    },
    MT: {
      name: "Malta"
    },
    MS: {
      name: "Montserrat"
    },
    MU: {
      name: "Mauritius"
    },
    NC: {
      name: "New Caledonia"
    },
    NR: {
      name: "Nauru"
    },
    PN: {
      name: "Pitcairn Islands"
    },
    PR: {
      name: "Puerto Rico"
    },
    PF: {
      name: "French Polynesia"
    },
    SG: {
      name: "Singapore"
    },
    SB: {
      name: "Solomon Islands"
    },
    ST: {
      name: "São Tomé and Principe"
    },
    SX: {
      name: "Saint Martin (Dutch)"
    },
    SC: {
      name: "Seychelles"
    },
    TC: {
      name: "Turks and Caicos Islands"
    },
    TO: {
      name: "Tonga"
    },
    TT: {
      name: "Trinidad and Tobago"
    },
    VC: {
      name: "Saint Vincent and the Grenadines"
    },
    VG: {
      name: "British Virgin Islands"
    },
    VI: {
      name: "United States Virgin Islands"
    },
    CY: {
      name: "Cyprus"
    },
    RE: {
      name: "Reunion (France)"
    },
    YT: {
      name: "Mayotte (France)"
    },
    MQ: {
      name: "Martinique (France)"
    },
    GP: {
      name: "Guadeloupe (France)"
    },
    CW: {
      name: "Curaco (Netherlands)"
    },
    IC: {
      name: "Canary Islands (Spain)"
    }
  },
  locations: {},
  labels: {},
  legend: {
    entries: []
  },
  regions: {
    "0": {
      states: [
        "FR",
        "BE"
      ],
      name: "RVJ"
    }
  }
};

simplemaps_worldmap_mapdata.locations = JSON.parse(locations);
