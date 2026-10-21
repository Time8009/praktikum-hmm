<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Eintrag bearbeiten</title>
    <link rel="stylesheet" href="{{ asset('css/styleu.css') }}">
    <script src="{{ asset('js/scriptu.js') }}" defer></script>
</head>
<body>
    <h1>Formular bearbeiten</h1>

    @if (count($errors) > 0)
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form id="form" action="/formular/{{ $entry->id }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <label for="first-name">*Vorname:</label>
        <input type="text" name="firstName" id="first-name" value="{{ old('firstName', $entry->first_name) }}">
        <span class="error-msg" id="error-first-name"></span><br><br>
        
        <label for="last-name">*Nachname:</label>
        <input type="text" name="lastName" id="last-name" value="{{ old('lastName', $entry->last_name) }}">
        <span class="error-msg" id="error-last-name"></span><br><br>
        
        <label for="birthdate">Geburtsdatum:</label>
        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $entry->birthdate) }}"><br><br>
        <span class="error-msg" id="error-birthdate"></span><br><br>
        
        <label for="email">*Email:</label>
        <input type="email" name="email" id="email" value="{{ old('email', $entry->email) }}">
        <span class="error-msg" id="error-email"></span><br><br>
        
        <label for="address">Adresse:</label>
        <input type="text" name="address" id="address" value="{{ old('address', $entry->address) }}"><br><br>
        
        <label for="zip-code">PLZ:</label>
        <input type="text" name="zipCode" id="zip" value="{{ old('zipCode', $entry->zip_code) }}">
        <span class="error-msg" id="error-zip"></span><br><br>
        
        <label for="city">Ort:</label>
        <input type="text" name="city" id="city" value="{{ old('city', $entry->city) }}">
        <span class="error-msg" id="error-city"></span><br><br>
        
        <label for="subject">*Betreff:</label>
        <input type="text" name="subject" id="subject" value="{{ old('subject', $entry->subject) }}">
        <span class="error-msg" id="error-subject"></span><br><br>
        
        <p class="message-label">Nachricht:</p>
        <textarea id="message" name="message" rows="5" cols="40">{{ old('message', $entry->message) }}</textarea><br><br>
        
        <input type="hidden" name="admin_password" id="admin_password" value="">
        <input type="submit" value="Absenden" name="submit" id="submit-btn">
    </form>

    <script>
        document.getElementById("submit-btn").addEventListener("click", function(event) {
            const pw = prompt("Bitte Admin-Passwort eingeben:");
            if (!pw) {
                event.preventDefault();
                return;
            }
            document.getElementById("admin_password").value = pw;
        });
    </script>
</body>
</html>