import { Outlet, Link } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";
function Menu() {
    const nav = useNavigate();
    const logout = () => {
        if (sessionStorage.getItem("sid") != undefined) { // if the user is logged in
            let data = new FormData();
            data.append("sid", sessionStorage.getItem("sid"));
            httpSrv.logout(data).then(
                res => { // remove the session id and the type, and jump to the login page
                    sessionStorage.removeItem("type");
                    sessionStorage.removeItem("sid");
                    alert(res.data);
                    nav("/");
                },
                rej => {
                    alert(rej);
                }
            )
        } else {
            alert("Login First");
        }
    }
    return (
        <>
            <nav className="navbar navbar-expand-sm" style={{ background: "darkgreen" }}>
                <div className="container-fluid justify-content-start">
                    <button
                        className="navbar-toggler d-lg-none"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapsibleNavId"
                        aria-controls="collapsibleNavId"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    ></button>
                    <div className="collapse navbar-collapse" id="collapsibleNavId">
                        <ul className="navbar-nav me-auto mt-2 mt-lg-0">
                            <li className="nav-item" style={{display: (sessionStorage.getItem("type") != undefined) ? "none" : "block" }}>
                                <Link to="/" className="nav-link fw-bolder" style={{ color: "white" }}>Login</Link>
                            </li>
                            <li className="nav-item" style={{display: (sessionStorage.getItem("type") != undefined && sessionStorage.getItem("type") === "Customer") ? "none" : "block" }}>
                                <Link to="/register" className="nav-link fw-bolder" style={{ color: "white" }}>Register</Link>
                            </li>
                            <li className="nav-item" style={{display: (sessionStorage.getItem("type") == undefined || sessionStorage.getItem("type") != "Admin") ? "none" : "block" }}>
                                <Link to="/stAppr" className="nav-link fw-bolder" style={{ color: "white" }}>Staff Approval List</Link>
                            </li>
                            <li className="nav-item" style={{display: (sessionStorage.getItem("type") == undefined) ? "none" : "block" }}>
                                <Link to="/blist" className="nav-link fw-bolder" style={{ color: "white" }}>Book List</Link>
                            </li>
                            <li className="nav-item" style={{display: (sessionStorage.getItem("type") == undefined || sessionStorage.getItem("type") === "Customer") ? "none" : "block" }}>
                                <Link to="/breg" className="nav-link fw-bolder" style={{ color: "white" }}>Book Registration</Link>
                            </li>
                        </ul>
                    </div>
                    <button type="button" className="btn btn-sm btn-success" onClick={logout} style={{ display: (sessionStorage.getItem("type") == undefined) ? "none" : "block" }}>Log out</button>
                </div>
            </nav>
            <div className="container- mt-4">
                <div className="row justify-content-center align-items-center g-2">
                    <div className="col-12">
                        <Outlet />
                    </div>
                </div>
            </div>
        </>
    )
}
export default Menu;