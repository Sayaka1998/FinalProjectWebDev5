import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";
function Login() {
  const [email, setEmail] = useState("");
  const [pass, setPass] = useState("");
  const [sid, setSid] = useState("");
  const [type, setType] = useState("");
  const nav = useNavigate();

  useEffect(() => {
    if (sid) {
      sessionStorage.setItem("sid", sid);
      sessionStorage.setItem("type", type);
      if (type === "Customer") {
        nav("/blist");
      } else {
        nav("/bookregister");
      }
    }
  }, [sid, type, nav]);

  const submitHandle = (e) => {
    e.preventDefault();
    let data = new FormData(e.target);
    httpSrv.login(data).then(
      (res) => {
        if (typeof res.data === "string") {
          alert(res.data);
        } else {
          setSid(res.data.sid);
          setType(res.data.type);
        }
      },
      (rej) => {
        alert(rej);
      }
    );
  };

  return (
    <>
      <h1 className="text-center fw-bolder mb-5">Welcome to Our Library</h1>
      <h3 className="text-center fw-bold mb-3">Login</h3>
      <div className="container-fluid">
        <div className="row justify-content-center align-items-center g-2">
          <div className="col-4">
            <form onSubmit={submitHandle}>
              <div className="mb-3">
                <select className="form-select form-select-lg" name="type">
                  <option defaultValue>Customer</option>
                  <option>Staff</option>
                  <option>Admin</option>
                </select>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="Email" />
                <label htmlFor="email">Email</label>
              </div>
              <div className="form-floating mb-3">
                <input type="password" className="form-control" name="pass" value={pass} onChange={(e) => setPass(e.target.value)} placeholder="Password" />
                <label htmlFor="pass">Password</label>
              </div>
              <button type="submit" className="btn btn-outline-primary">
                Login
              </button>
            </form>
          </div>
        </div>
      </div>
    </>
  );
}

export default Login;
