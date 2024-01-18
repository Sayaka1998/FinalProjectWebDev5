import { Outlet, Link } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";
function Menu() {
  const nav = useNavigate();
  const logout = () => {
    if (sessionStorage.getItem("sid") != undefined) {
      let data = new FormData();
      data.append("sid", sessionStorage.getItem("sid"));
      httpSrv.logout(data).then(
        (res) => {
          sessionStorage.removeItem("type");
          sessionStorage.removeItem("sid");
          alert(res.data);
          nav("/");
        },
        (rej) => {
          alert(rej);
        }
      );
    } else {
      alert("Login First");
    }
  };
  return (
    <>
      <nav className="navbar navbar-expand-sm" style={{ background: "darkgreen" }}>
        <div className="container-fluid justify-content-start">
          <button className="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
            <span className="navbar-toggler-icon"></span>
          </button>
          <div className="collapse navbar-collapse" id="collapsibleNavId">
            <ul className="navbar-nav me-auto mt-2 mt-lg-0">
              <li className="nav-item">
                <Link to="/" className="nav-link fw-bolder" style={{ color: "white" }} href="#">
                  Login
                </Link>
              </li>
              <li className="nav-item">
                <Link to="/register" className="nav-link fw-bolder" style={{ color: "white" }} href="#">
                  Register
                </Link>
              </li>
              <li className="nav-item">
                <Link to="/stAppr" className="nav-link fw-bolder" style={{ color: "white" }} href="#">
                  Staff Approval List
                </Link>
              </li>
              <li className="nav-item">
                <Link to="/blist" className="nav-link fw-bolder" style={{ color: "white" }} href="#">
                  Book List
                </Link>
              </li>
              <li className="nav-item">
                <Link to="/bookregister" className="nav-link fw-bolder" style={{ color: "white" }} href="#">
                  Book Registration
                </Link>
              </li>
            </ul>
            <button type="button" className="btn btn-sm btn-success" onClick={logout}>
              Log out
            </button>
          </div>
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
  );
}
export default Menu;
