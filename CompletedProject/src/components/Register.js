import { useState } from "react";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";

function Register() {
    const [fname, setFname] = useState("");
    const [lname, setLname] = useState("");
    const [email, setEmail] = useState("");
    const [pass, setPass] = useState("");
    const [type, setType] = useState("");

    const nav = useNavigate();

    // send a form data to backend
    const submitHandle = (e) => {
        e.preventDefault();
        let data = new FormData(e.target);
        httpSrv.register(data)
            .then((res) => {
                console.log(res);
            })
            .catch((e) => {
                console.log(e);
            });
        alert("Registered Successfully");
        if(sessionStorage.getItem("sid") == undefined) {
            nav("/");
        }
    };

    return (
        <>
            <h1 className="text-center fw-bolder">Register Page</h1>
            <div className="container-fluid mt-5">
                <div className="row justify-content-center align-items-center g-2">
                    <div className="col-4">
                        <form onSubmit={submitHandle}>
                            <div className="mb-3">
                                <select className="form-select form-select-lg" name="type" value={type} onChange={(e) => setType(e.target.value)}>
                                    <option value="Customer">Customer</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="fname" value={fname} onChange={(e) => setFname(e.target.value)} placeholder="First Name" />
                                <label htmlFor="fname">First Name</label>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="lname" value={lname} onChange={(e) => setLname(e.target.value)} placeholder="Last Name" />
                                <label htmlFor="lname">Last Name</label>
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
                                Register
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}

export default Register;
