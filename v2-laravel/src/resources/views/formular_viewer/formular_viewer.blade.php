<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Einträge</title>
    <link rel="stylesheet" href="{{ asset('css/stylev.css') }}">
</head>
<body>
    @if(session('success'))
        <p style="color: green; text-align: center;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red; text-align: center;">{{ session('error') }}</p>
    @endif

    <form id="action-form" method="POST">
        @csrf
        <input type="hidden" name="admin_password" id="admin_password" value="">
        <table>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Geburtsdatum</th>
                <th>E-Mail</th>
                <th>Adresse</th>
                <th>PLZ</th>
                <th>Stadt</th>
                <th>Betreff</th>
                <th>Nachricht</th>
            </tr>
            @forelse ($entries as $row)
            <tr>
                <td><input type="radio" name="selected" value="{{ $row->id }}"></td>
                <td>{{ $row->id }}</td>
                <td>{{ $row->first_name }}</td>
                <td>{{ $row->last_name }}</td>
                <td>{{ $row->birthdate ? date('d.m.Y', strtotime($row->birthdate)) : '-' }}</td>
                <td>{{ $row->email }}</td>
                <td>{{ $row->address }}</td>
                <td>{{ $row->zip_code }}</td>
                <td>{{ $row->city }}</td>
                <td>{{ $row->subject }}</td>
                <td>{!! nl2br(e($row->message)) !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center;">Keine Einträge vorhanden.</td>
            </tr>
            @endforelse
        </table>

        <button type="submit" formaction="/entries/delete" id="delete">Löschen</button>
        <button type="button" id="change">Ändern</button>
        <button type="button" id="reload" onclick="window.location.reload();">Tabelle Neuladen</button>
        <button type="submit" formaction="/entries/truncate" id="reset">Tabelle Zurücksetzen</button>
    </form>

    <p style="text-align: center; margin-top: 15px;">
        <a href="/formular">Neuen Eintrag erstellen</a>
    </p>

    <script>
        document.getElementById("delete").addEventListener("click", function(event) {
            const selected = document.querySelector('input[name="selected"]:checked');
            if (!selected) {
                alert("Bitte wählen Sie einen Eintrag aus.");
                event.preventDefault();
                return;
            }
            const pw = prompt("Bitte Admin-Passwort eingeben:");
            if (!pw) {
                event.preventDefault();
                return;
            }
            document.getElementById("admin_password").value = pw;
        });

        document.getElementById("reset").addEventListener("click", function(event) {
            const pw = prompt("Bitte Admin-Passwort eingeben:");
            if (!pw) {
                event.preventDefault();
                return;
            }
            document.getElementById("admin_password").value = pw;
        });

        document.getElementById("change").addEventListener("click", function() {
            const selected = document.querySelector('input[name="selected"]:checked');
            if (!selected) {
                alert("Bitte wählen Sie einen Eintrag aus.");
                return;
            }
            window.location.href = "/formular/" + selected.value + "/edit";
        });
    </script>
</body>
</html>