import './Style.css';

function NavBar() {
  return (
    <>
              <nav class="navbar navbar-light bg-primary justify-content-between">
               <a className="navbar-brand">My Contact List</a>
                <form class="form-inline">
                  <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"></input>
                      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
              </nav>
    </>
  );
}

export default NavBar;