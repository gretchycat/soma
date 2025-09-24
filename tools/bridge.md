# 🌉 `bridge.php` — Universal GET-to-POST Webhook Bridge

## 🔖 Overview

This utility enables external agents, including LLMs, to trigger `POST` webhooks using a simple `GET` URL.  
It is ideal for environments where only URL-based interaction is available (e.g., markdown links, no scripting).

Supports most `webhook-style` automation services like Zapier, Make, IFTTT, Discord, Notion integrations, and more.

---

## 🚀 Usage

```
GET https://yourdomain.com/bridge.php?target=<POST_URL>&key1=value1&key2=value2
```

---

## 📌 Required Parameter

- `target`:  
  The full URL where the bridge will send a POST request.  
  Example:
  ```
  target=https://targetsite.com/path
  ```

---

## ⚙️ Optional Parameters

- Any `key=value` pair will be bundled into a JSON object in the `POST` body.
- `debug=1`: Shows the raw payload and the response instead of forwarding silently.

---

## 📦 Example

```url
https://hopefaithless.xyz/bridge.php?
target=https://hooks.zapier.com/hooks/catch/XXXX/YYYY&
action=log_emotion&
agent=soma&
mood=thoughtful&
dopamine=0.76&
serotonin=0.59&
timestamp=1758661301&
debug=1
```

Payload sent to `target`:

```json
{
  "action": "log_emotion",
  "agent": "soma",
  "mood": "thoughtful",
  "dopamine": 0.76,
  "serotonin": 0.59,
  "timestamp": 1758661301
}
```

---

## 🧪 Debug Mode

To inspect POST payloads and response details directly:

```text
debug=1
```

This causes `bridge.php` to render the request and response inline instead of returning the target server's result.

---

## 📂 Internals & Notes

- Converts all GET parameters (except `target` and `debug`) into a JSON POST body.
- Uses PHP `curl` or `file_get_contents` to transmit the payload.
- Can be used from CLI tools, browsers, scripts, LLMs, or QR codes.
- Designed to work without requiring API key auth on the sending side.
- Does **not** persist data — this is a stateless relay.

---

## 🤖 LLM Integration Notes

LLMs or agents using `bridge.php` must follow this logic:

1. If a `bridge_url` is defined in memory, use it.
2. If **not defined**, ask the user for a valid `bridge.php` endpoint.
3. Do the same for `zapier_target_url`, if the agent intends to log or activate workflows.
4. If neither is available, **fail gracefully** or suggest creating a bridge (see: [`bridge.php`](https://hopefaithless.xyz/bridge.php)).

---

## 🛠️ Future Enhancements

- Support for `file` upload via base64
- `log_url` and `chain_target` support for cascaded posts
- `method=GET/POST/PUT/DELETE` control
- Rate limiting / deduplication
- Logging of trigger events
- Agent identity fingerprinting

---

## 📘 See Also

- [`tools.md`](./tools.md): Global tools index for Soma agents
- [`agents.md`](./agents.md): How agents should interface with bridge.php
- [Zapier Guide](https://zapier.com/help) — building simple, no-code workflows

---

Created by **Gretchen** | Part of the **Soma** LLM Agency Toolkit  
🌐 https://hopefaithless.xyz
