import http from "../httpcommon";

class RegService {
  register(data) {
    return http.post("/register", data);
  }
}
export default new RegService();
