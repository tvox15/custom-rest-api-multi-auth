//// JS DATA VALIDATION ////


document.getElementById("submit").addEventListener("click", function(event) {
    // get UIC number field value

    var UICNumberField = document.getElementById("uic-number-field").value;

    // Create regex to only allow numbers and letters to be submitted
    var reg = new RegExp('^[a-zA-Z0-9]+$');

    // ERROR CHECK 1 // check to verify field is not empty
    if (UICNumberField === '') {
        //set error message
        document.getElementById("error").innerHTML = "You must enter a UIC Number";
        // prevent POST 
        event.preventDefault()
    }

    // ERROR CHECK 2 // check to verify UIC number is 20 characters
    else if (UICNumberField.length !== 20) {
        //set error message
        document.getElementById("error").innerHTML = "No UIC found. Try searching again.";
        // prevent POST 
        event.preventDefault()
    }
});