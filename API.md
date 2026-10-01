# ClickChin landing API

Base URL: `http://localhost:8000/api/v1`. JSON requests use `Content-Type: application/json`. Authenticated endpoints require `Authorization: Bearer <local token>` obtained from login.

| Method | Path | Purpose | Authentication |
| --- | --- | --- | --- |
| POST | `/auth/register` | Register a user | None |
| POST | `/auth/login` | Obtain a token | None |
| POST | `/auth/logout` | Revoke the current token | Bearer |
| GET | `/auth/validateToken` | Check the current token | Bearer |
| GET, POST | `/components` | List or create the current user's components | Bearer |
| GET, PUT, DELETE | `/components/{component}` | Read, update, or delete an owned component | Bearer |
| GET, POST | `/landings` | List or create the current user's landings | Bearer |
| GET, PUT, DELETE | `/landings/{landing}` | Read, update, or delete an owned landing | Bearer |
| GET | `/landings/user/{user_id}` | List an owned user's landings | Bearer |
| POST | `/landings/upload-media` | Upload one JPEG/PNG image, max 2 MiB | Bearer |

For component creation, send `name` (string), `type` (string), and `componentData` (JSON array/object). For landing creation or update, send `landingData` (JSON array/object). The server assigns `user_id`; clients cannot choose an owner. Updates to components may include any subset of the three fields. A missing resource returns 404, a different user's resource returns 403, validation errors return 422, and unauthenticated access returns 401. Upload the file as multipart field `media`; the response contains a relative `url`.

The API stores builder state. It does not implement full ClickChin generation, export, or deployment.
