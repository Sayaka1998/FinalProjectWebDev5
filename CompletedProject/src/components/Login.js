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
            // store the session id and the user type to the session storage
            sessionStorage.setItem("sid", sid);
            sessionStorage.setItem("type", type);
            nav("/blist");
        }
    }, [sid, type, nav]);

    const submitHandle = (e) => { // send the input user data to the back-end 
        e.preventDefault();
        let data = new FormData(e.target);
        httpSrv.login(data).then(
            res => {
                if (typeof res.data === 'string') { // if res.data if a message
                    alert(res.data);
                } else { // if res.data is a associate array, set it as session id and type
                    setSid(res.data.sid);
                    setType(res.data.type);
                }
            },
            rej => {
                alert(rej);
            }
        );
    }

    return (
        <>
            <h1 className="text-center fw-bolder">Login Page</h1>
            <div className="container-fluid mt-5">
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
                                <input type="text" className="form-control" name="email" value={email} onChange={e => setEmail(e.target.value)} placeholder="Email" />
                                <label htmlFor="email">Email</label>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="password" className="form-control" name="pass" value={pass} onChange={e => setPass(e.target.value)} placeholder="Password" />
                                <label htmlFor="pass">Password</label>
                            </div>
                            <button type="submit" className="btn btn-outline-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Login;