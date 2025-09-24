<?php

// Universal GET-to-POST Bridge by Gretchen

main();

function main() {
    $debug = isset($_GET['debug']);

    // 1. Show usage if no parameters given
    if (count($_GET) === 0) {
        show_usage();
        return;
    }

    // 2. Extract target URL
    $target = get_target_url();
    if (!$target) {
        respond_with_error("Missing target URL. Use ?target=https://...", $debug);
        return;
    }

    // 3. Build payload
    $payload = build_payload();
    $method = "POST";
    $headers = [
        'Content-Type: application/json',
        'User-Agent: Universal-Bridge/1.0'
    ];

    // 4. Forward request
    $response = forward_request($target, $method, $payload, $headers);

    // 5. Output
    if ($debug) {
        debug_output($target, $method, $headers, $payload, $response);
    } else {
        header("Content-Type: application/json");
        echo $response;
    }
}

/**
 * Shows usage help
 */
function show_usage() {
    header("Content-Type: text/plain");
    echo "
🌐 Universal GET-to-POST Bridge

USAGE:
  GET https://yourdomain.com/Proxy.php?target=<POST_URL>&key1=value1&key2=value2

REQUIRED:
  - target=https://example.com/webhook  (POST destination)

OPTIONAL:
  - Any key=value parameters will be converted into JSON POST body
  - debug=1 will print debug info instead of returning raw response

EXAMPLE:
  https://yourdomain.com/bridge.php?target=https://hooks.zapier.com/hooks/catch/...&action=log&user=Gretchen&debug=1

NOTES:
  - All parameters are bundled into JSON and sent via POST
  - Supports most webhook-style use cases
";
}

/**
 * Extracts target URL
 */
function get_target_url() {
    return isset($_GET['target']) ? urldecode($_GET['target']) : null;
}

/**
 * Builds the JSON payload from all non-reserved GET params
 */
function build_payload() {
    $data = $_GET;
    unset($data['target'], $data['debug']);
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * Forwards POST request to destination
 */
function forward_request($target, $method, $payload, $headers) {
    $ch = curl_init($target);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return json_encode(["error" => $error]);
    }

    curl_close($ch);
    return $result;
}

/**
 * Prints debug information
 */
function debug_output($target, $method, $headers, $payload, $response) {
    header("Content-Type: text/plain");
    echo "--- Universal Bridge Debug ---\n";
    echo "Target:   $target\n";
    echo "Method:   $method\n";
    echo "Headers:  " . implode(", ", $headers) . "\n";
    echo "Payload:  $payload\n";
    echo "Response: $response\n";
}

/**
 * Returns clean error
 */
function respond_with_error($message, $debug) {
    if ($debug) {
        header("Content-Type: text/plain");
        echo "❌ ERROR: $message\n";
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => $message]);
    }
}
?>
