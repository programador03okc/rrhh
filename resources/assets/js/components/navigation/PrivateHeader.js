import React from 'react';
import { Redirect } from 'react-router-dom';
import {
    BrowserRouter as Router,
    Route,
    Link,
    withRouter
} from 'react-router-dom';
import fakeAuth from '../../modules/sistema/usuario/services/Auth';

const PrivateHeader = () => {
    return (
        <nav className="navbar navbar-expand-lg navbar-light navbar-okc">
            <a className="navbar-brand" href="#" />
            <button
                className="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarText"
                aria-controls="navbarText"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span className="navbar-toggler-icon" />
            </button>
            <div className="collapse navbar-collapse" id="navbarText">
                <ul className="navbar-nav mr-auto">
                    <li className="nav-item">
                        <Link className="nav-link" to="/modulos">
                            Módulos
                        </Link>
                    </li>

                    <li className="nav-item">
                        <a className="nav-link" href="./config">
                            Configuración
                        </a>
                    </li>
                </ul>

                {/* <span className="navbar-text"> <Link className="nav-link" to="/salir">Cerrar Sesión</Link></span> */}
                {JSON.parse(sessionStorage.getItem('userSession')) ? (
                    <AuthButton />
                ) : (
                    <Redirect to="/" />
                )}
            </div>
        </nav>
    );
};
export default PrivateHeader;

const AuthButton = withRouter(({ history }) =>
    JSON.parse(sessionStorage.getItem('userSession')).estado ? (
        <ul className="nav navbar-nav navbar-right" id="ddb">
            <li className="dropdown">
                <a
                    href="#"
                    className="dropdown-toggle"
                    data-toggle="dropdown"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i className="fas fa-user-circle fa-lg" />
                    <span className="navbar-text">
                        {
                            JSON.parse(sessionStorage.getItem('userSession'))
                                .nombre
                        }{' '}
                        {
                            JSON.parse(sessionStorage.getItem('userSession'))
                                .apellidos
                        }
                    </span>
                </a>
                <ul className="dropdown-menu">
                    <li>
                        <a href="javascript:void(0);">
                            <span className="glyphicon glyphicon-cog" />{' '}
                            Configuracion
                        </a>
                    </li>
                    <li>
                        <a
                            href="javascript:void(0);"
                            onClick={() => {
                                fakeAuth.signout(() => history.push('/'));
                            }}
                        >
                            <span className="glyphicon glyphicon-log-out" />{' '}
                            Cerrar Sessión
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    ) : (
        <div>
            <span className="navbar-text">No identificado</span>
        </div>
    )
);
