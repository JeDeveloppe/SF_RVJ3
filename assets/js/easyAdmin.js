let switchBoiteIsDetachee = document.getElementById('Boite_isOnline');
let switchBoiteIsOccasion = document.getElementById('Boite_isOccasion');

if(switchBoiteIsDetachee){
    
    switchBoiteIsDetachee.addEventListener('change', function() {
        let saveAndContinuButton = document.getElementsByClassName('action-saveAndContinue');
        saveAndContinuButton[0].click();
    });
}

if(switchBoiteIsOccasion){
    
    switchBoiteIsOccasion.addEventListener('change', function() {
        let saveAndContinuButton = document.getElementsByClassName('action-saveAndContinue');
        saveAndContinuButton[0].click();
    });
}
