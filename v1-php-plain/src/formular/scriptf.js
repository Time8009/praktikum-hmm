const form = document.getElementById("form");
const firstName = document.getElementById("first-name");
const lastName = document.getElementById("last-name");
const subject = document.getElementById("subject");
const email = document.getElementById("email");
const zipCode = document.getElementById("zip");
const city = document.getElementById("city");
const birthdate = document.getElementById("birthdate");

const errorFirstName = document.getElementById("error-first-name");
const errorLastName = document.getElementById("error-last-name");
const errorSubject = document.getElementById("error-subject");
const errorZipCode = document.getElementById("error-zip");
const errorEmail = document.getElementById("error-email");
const errorCity = document.getElementById("error-city");
const errorBirthdate = document.getElementById("error-birthdate");

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function checkField(input, errorSpan, type) 
{
    const val = input.value.trim();

    if (val === "" && type !== "zip" && type !== "city" && type !== "birthdate") 
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Dieses Feld ist ein Pflichtfeld";
        return false;
    }
    else if (val.length < 2 && (type === "firstName" || type === "lastName"))
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Der Name muss mindestens 2 Zeichen lang sein!";
        return false;
    }
    else if (type === "email" && val !== "" && !emailRegex.test(val))
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Bitte geben Sie eine gültige E-Mail-Adresse ein!";
        return false;
    }
    else if (type === "zip" && val !== "" && !/^\d{5}$/.test(val))
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Die PLZ muss aus genau 5 Ziffern bestehen!";
        return false;
    }
    else if (type === "city" && val !== "" && val.length < 3)
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Die Stadt muss mindestens 3 Zeichen lang sein!";
        return false;
    }
    else if (type === "birthdate" && val !== "" && new Date(val) > new Date())
    {
        input.classList.add("is-invalid");
        errorSpan.textContent = "* Das Geburtsdatum darf nicht in der Zukunft liegen!";
        return false;
    }
    else
    {
        input.classList.remove("is-invalid");
        errorSpan.textContent = "";
        return true;
    }
}

firstName.addEventListener("blur", function() 
{
    checkField(firstName, errorFirstName, "firstName");
});

lastName.addEventListener("blur", function() 
{
    checkField(lastName, errorLastName, "lastName");
});

subject.addEventListener("blur", function() 
{
    checkField(subject, errorSubject, "subject");
});

email.addEventListener("blur", function()
{
    checkField(email, errorEmail, "email");
});

zipCode.addEventListener("blur", function()
{
    checkField(zipCode, errorZipCode, "zip");
});

city.addEventListener("blur", function()
{
    checkField(city, errorCity, "city");
});

birthdate.addEventListener("blur", function()
{
    checkField(birthdate, errorBirthdate, "birthdate");
});

form.addEventListener("submit", function(event) 
{
    const isFirstNameValid = checkField(firstName, errorFirstName, "firstName");
    const isLastNameValid  = checkField(lastName, errorLastName, "lastName");
    const isSubjectValid   = checkField(subject, errorSubject, "subject");
    const isEmailValid     = checkField(email, errorEmail, "email");
    const isZipValid       = checkField(zipCode, errorZipCode, "zip");
    const isCityValid      = checkField(city, errorCity, "city");
    const isBirthdateValid = checkField(birthdate, errorBirthdate, "birthdate");

    if (!isFirstNameValid || !isLastNameValid || !isSubjectValid || !isEmailValid || !isZipValid || !isCityValid || !isBirthdateValid) 
    {
        event.preventDefault();
    }
});