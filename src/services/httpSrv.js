import http from "../httpcommon";

class httpSrv {
  login(data) {
    return http.post("/login", data);
  }
  logout(data) {
    return http.post("/logout", data);
  }
  alist(data) {
    return http.post("/alist", data);
  }
  approve(data) {
    return http.post("/approve", data);
  }
  blist(data) {
    return http.post("/blist", data);
  }
  borrow(data) {
    return http.post("/borrow", data);
  }
  register(data) {
    return http.post("/register", data);
  }
  bookregister(data) {
    return http.post("/bookregister", data);
  }
}

export default new httpSrv();
