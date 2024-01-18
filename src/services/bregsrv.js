import http from "../httpcommon";

class Bregsrv {
  bookregister(data) {
    return http.post("/bookregister", data);
  }
}
export default new Bregsrv();
