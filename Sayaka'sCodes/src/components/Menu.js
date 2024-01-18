import { Outlet, Link } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";
function Menu() {
    const nav = useNavigate();
    const logout = () => {
        if(sessionStorage.getItem("sid") != undefined) {
            let data = new FormData();
            data.append("sid",sessionStorage.getItem("sid"));
            httpSrv.logout(data).then(
                res => {
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
    return(
        <>           
            <nav className="navbar navbar-expand-sm bg-secondary">
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
                    <li className="nav-item">
                        <Link to="/" className="nav-link" href="#">Login</Link>
                    </li>
                    <li className="nav-item">
                        <Link to="/stAppr" className="nav-link">Staff Approval List</Link>
                    </li>
                    <li className="nav-item">
                        <Link to="/blist" className="nav-link">Book List</Link>
                    </li>
                    <li className="nav-item">
                        <Link to="/breg" className="nav-link">Book Registration</Link>
                    </li>
                </ul>
            </div>
            <button type="button" className="btn btn-outline-danger" onClick={logout}>Log out</button>
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