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
}

export default new httpSrv();