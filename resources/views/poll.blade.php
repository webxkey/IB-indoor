<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Polling Status</title>
    <style>body{font-family:system-ui,Segoe UI,Roboto,Arial; padding:20px}</style>
</head>
<body>
    <h1>Polling Status</h1>
    <div id="status">Initializing...</div>

    <script>
    (function poll(){
        var el = document.getElementById('status');
        fetch("{{ route('poll.status') }}")
            .then(function(res){ if(!res.ok) throw res; return res.json(); })
            .then(function(json){ el.textContent = json.message + ' (at ' + json.timestamp + ')'; })
            .catch(function(){ el.textContent = 'Error fetching status'; })
            .finally(function(){ setTimeout(poll, 3000); });
    })();
    </script>
</body>
</html>
