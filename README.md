# optGuard Plugin WordPress
Add optGuard anti-fraud protection to WordPress sites. [optGuard API](https://api.optguard.com/doc/).

## Getting started

First, configure your optGuard Account credentials under `Settings > optGuard Anti-Fraud` in the Dashboard.

Second, let optGuard do our thing, sit back, and relax while we combat fraud for you!

That's it! :)

**Required Fields**

- `Acess Key` - Your Access Key from optGuard.
- `Secret Key` - Your Secret Key from optGuard.

For those who are interested in "how it all works," here's some insight:

**API Details**

This plugin currently uses the following optGuard API endpoint(s).

### `/v1/check`

Check an IP Address.

**HTTP Method:** `GET`

**Endpoint**

```bash
https://api.optguard.com/v1/check
```

**Parameters**

Pass these along as query string parameters on your endpoint.

| Field       | Type   | Description                | Example     | Required |
|-------------|--------|----------------------------|-------------|----------|
| `access_key`| String | Enter your Access Key      | `123456`    | Yes      |
| `secret_key`| String | Enter your Secret Key      | `987`       | Yes      |
| `ip`        | String | IP Address to be checked   | `127.0.0.1` | Yes      |

**Sample API Calls**

```bash
# Check an IP Address (async, non-blocking)
https://api.optguard.com/v1/check?access_key=123456&secret_key=987&ip=127.0.0.1
```

**Sample Response**

The API will return a JSON object with a msg, status_msg, and status.

```json
{
    "msg": "IP is on the Blacklist",
    "status_msg": "IP_SUSPECT",
    "status": 1
}
```
