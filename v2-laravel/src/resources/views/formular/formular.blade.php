<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Formular</title>
    <link rel="stylesheet" href="{{ asset('css/stylef.css') }}">
    <script src="{{ asset('js/scriptf.js') }}" defer></script>
</head>
<body>
    <h1>Formular</h1>

    @if ($errors)
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="form" action="/formular" method="POST" novalidate>
        @csrf
        
        <label for="first-name">*Vorname:</label>
        <input type="text" name="firstName" id="first-name" value="{{ old('firstName') }}">
        <span class="error-msg" id="error-first-name"></span><br><br>
        
        <label for="last-name">*Nachname:</label>
        <input type="text" name="lastName" id="last-name" value="{{ old('lastName') }}">
        <span class="error-msg" id="error-last-name"></span><br><br>
        
        <label for="birthdate">Geburtsdatum:</label>
        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}"><br><br>
        <span class="error-msg" id="error-birthdate"></span><br><br>
        
        <label for="email">*Email:</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}">
        <span class="error-msg" id="error-email"></span><br><br>
        
        <label for="address">Adresse:</label>
        <input type="text" name="address" id="address" value="{{ old('address') }}"><br><br>
        
        <label for="zip-code">PLZ:</label>
        <input type="text" name="zipCode" id="zip" value="{{ old('zipCode') }}">
        <span class="error-msg" id="error-zip"></span><br><br>
        
        <label for="city">Ort:</label>
        <input type="text" name="city" id="city" value="{{ old('city') }}">
        <span class="error-msg" id="error-city"></span><br><br>
        
        <label for="subject">*Betreff:</label>
        <input type="text" name="subject" id="subject" value="{{ old('subject') }}">
        <span class="error-msg" id="error-subject"></span><br><br>
        
        <p class="message-label">Nachricht:</p>
        <textarea id="message" name="message" rows="5" cols="40" placeholder="Schreiben Sie hier Ihre Nachricht...">{{ old('message') }}</textarea><br><br>
        
        <input type="submit" value="Absenden" id="submit-btn">
    </form>
</body>
</html>