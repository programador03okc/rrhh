import React, { Component } from "react";
import PropTypes from "prop-types";
import API from "../../services/api/http";

class UploadFile extends Component {
    constructor(props) {
        super(props);

        this.state = {
            files: [],

            headerFile: [],
            lastFileName: "",
            adjunto: [
                {
                    archivo_archivo: "",
                    archivo_storage: "",
                    archivo_id_archivo: "",
                    id_detalle_requerimiento: "",
                    archivo_id_detalle_requerimiento: "",
                    archivo_fecha_registro: "",
                    archivo_estado: ""
                }
            ]
        };
        this.handleChangeFile = this.handleChangeFile.bind(this);
        this.UploadNow = this.UploadNow.bind(this);
        this.DeleteOneFile = this.DeleteOneFile.bind(this);
        this.DeleteFile = this.DeleteFile.bind(this);
        this.getArchivosAdjuntos = this.getArchivosAdjuntos.bind(this);
    }

    handleChangeFile(event) {
        const newObject = this.state.files;
        newObject.push(event.target.files[0]);
        this.setState({ files: newObject });

        const newObject2 = this.state.headerFile;
        newObject2.push({
            archivo_archivo: event.target.files[0].name,
            archivo_id_archivo: "",
            archivo_id_detalle_requerimiento: this.props
                .id_detalle_requerimiento,
            archivo_fecha_registro: "",
            archivo_estado: 1
        });
        this.setState({
            headerFile: newObject2,
            lastFileName: event.target.files[0].name
        });
    }

    componentDidMount() {
        this.getArchivosAdjuntos();
    }

    getArchivosAdjuntos() {
        let id_detalle_requerimiento = this.props.id_detalle_requerimiento;
        if(id_detalle_requerimiento !== undefined && id_detalle_requerimiento > 0){

            API.get(`sistema/mostrar_archivos_adjuntos/${id_detalle_requerimiento}`)
            .then(res => {
                if (res.status === 200) {
                    if (res.data.length > 0) {
                        console.log("res.data", res.data);
                        this.setState({
                            headerFile: res.data
                        });
                    }
                } else {
                    console.log("nose puede cargar la data", res);
                }
            })
            .catch(function(error) {
                if (error.response) {
                    // La solicitud se realizó y el servidor respondió con un código de estado
                    // que cae fuera del rango de 2xx
                    console.log(error.response.data);
                    console.log(error.response.status);
                    console.log(error.response.headers);
                } else if (error.request) {
                    // La solicitud se realizó pero no se recibió respuesta
                    // `error.request` es una instancia de XMLHttpRequest en el navegador y una instancia de
                    // http.ClientRequest en node.js
                    console.log(error.request);
                } else {
                    // Algo sucedió al configurar la solicitud que desencadenó un error
                    console.log("Error", error.message);
                }
                console.log(error.config);
            });
            
        }else{
            alert("Error, el id de detalle de requerimiento no esta definido");
        }
    }

    // static getDerivedStateFromProps(nextProps, prevState){
    //     console.log(nextProps.adjunto.length);
    //     if(nextProps.adjunto.length>0 ) {
    //     if(nextProps.adjunto[0].archivo_archivo !== prevState.adjunto[0].archivo_archivo || nextProps.adjunto.length !== prevState.adjunto.length ) {
    //         //  if(Object.keys(nextProps.adjunto).length !== Object.keys(prevState.headerFile).length ) {
    //         return {
    //             headerFile: nextProps.adjunto
    //         };
    //     }
    //     }else{

    //         return null;
    //     }
    // }

    // shouldComponentUpdate(nextProps, nextState) {
    //     if(Object.keys(nextProps.options).length !== Object.keys(nextState.options).length ) {
    //         if(nextProps.options.nombre_carpeta_destino == nextState.options.nombre_carpeta_destino){
    //             return true;
    //         }
    //     }
    //     return false;
    // }

    UploadNow() {
        let action = `/sistema/uploadfile`;
        var formData = new FormData();
        if (this.state.files.length > 0) {
            this.state.files.forEach((element, key) => {
                formData.append(
                    "archivo_adjunto[]",
                    this.state.files ? this.state.files[key] : ""
                );
            });
        } else {
            alert("No hay archivo adjunto");
        }
        formData.append(
            "nombre_carpeta_destino",
            this.props.nombre_carpeta_destino
                ? this.props.nombre_carpeta_destino
                : ""
        );
        formData.append(
            "abreviatura",
            this.props.abreviatura ? this.props.abreviatura : ""
        );
        formData.append(
            "id_detalle_requerimiento",
            this.props.id_detalle_requerimiento
                ? this.props.id_detalle_requerimiento
                : 0
        );

        for (var pair of formData.entries()) {
            console.log(pair);
        }
        let method = "post";
        const config = {
            method: method,
            url: action,
            headers: { "Content-Type": "multipart/form-data" },
            responseType: "json",
            data: formData
        };
        API.request(config)
            .then(res => {
                // console.log(res.data);
                if (res.status === 200) {
                    alert("Se guardo correctamente el archivo");
                    this.getArchivosAdjuntos();
                    this.setState({ cantidadArchivos: res.data });
                    this.props.getCantidadArchivos(res.data);
                }
            })
            .catch(function(error) {
                if (error.response) {
                    console.log(error.response.data);
                    console.log(error.response.status);
                    console.log(error.response.headers);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log("Error", error.message);
                }
                console.log(error.config);
            });
    }

    DeleteFile(index, id) {
        console.log("metodo para eliminar de la base de datos!");
        let action = "/sistema/actualizar_status_file";
        let method = "put";
        // if (this.state.prevMode === 'editar'){
        //     method = 'put';
        //     action = 'logistica/actualizar_cotizacion/'+detalle_grupoCotizacion.id_detalle_grupo_cotizacion;
        // }
        const config = {
            method: method,
            url: action,
            headers: { "Content-Type": "application/json" },
            responseType: "json",
            data: id
        };
        API.request(config)
            .then(res => {
                if (res.status === 200) {
                    console.log(res.data);
                    if (res.data > 0) {
                        alert("Se actualizo el estado a 'Eliminado'");
                    }
                    var sel = document.getElementById(index);
                    sel.remove(1);
                }
            })
            .catch(function(error) {
                if (error.response) {
                    console.log(error.response.data);
                    console.log(error.response.status);
                    console.log(error.response.headers);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log("Error", error.message);
                }
                console.log(error.config);
            });
    }

    DeleteOneFile(index, id_archivo) {
        var r = confirm(`Está seguro que desea eliminar esta archivo ?`);
        if (r === true) {
            let archivos = this.state.files;
            var removeIndex = archivos
                .map(function(item, indice) {
                    return indice;
                })
                .indexOf(index);
            archivos.splice(removeIndex, 1);
            this.setState({ files: archivos });

            const that = this;
            if (id_archivo !== null || id_archivo > 0) {
                that.DeleteFile(index, id_archivo);
            }
        } else {
        }
    }

    render() {
        let headerFile = this.state.headerFile.map((item, index) => {
            if (item.archivo_estado > 0) {
                return (
                    <div className="col-auto" key={index} id={index}>
                        <div
                            className="alert alert-primary alert-dismissible fade show"
                            role="alert"
                        >
                            <a
                                href={
                                    "/api/sistema/downloadfile/" +
                                    this.props.nombre_carpeta_destino +
                                    item.archivo_archivo
                                }
                            >
                                {item.archivo_archivo}
                            </a>
                            <button
                                type="button"
                                className="close"
                                aria-label="Close"
                                onClick={() => {
                                    this.DeleteOneFile(
                                        index,
                                        item.archivo_id_archivo
                                    );
                                }}
                            >
                                <span aria-hidden="true">
                                    <i className="fas fa-times" />
                                </span>
                            </button>
                        </div>
                    </div>
                );
            }
        });

        return (
            <div>
                <div className="form-group">
                    {/* <label>Adjunto Archivo</label>  */}
                    <div className="input-group mb-3">
                        <div className="custom-file">
                            <input
                                type="file"
                                className="custom-file-input"
                                name="archivo_adjunto"
                                onChange={this.handleChangeFile}
                                // value={this.state.ObjRequerimiento.archivo_adjunto?this.state.ObjRequerimiento.archivo_adjunto:''}
                            />
                            <label className="custom-file-label btn-ico-file_add">
                                {this.state.lastFileName}
                            </label>
                        </div>

                        {/* &nbsp; */}
                        {/* {this.state.ObjRequerimiento.archivo_adjunto?(
                                <div className="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <button type="button" className="btn btn-primary"  title={this.state.ObjRequerimiento.archivo_adjunto?this.state.ObjRequerimiento.archivo_adjunto:''}><i className="fas fa-file-download fa-2x"></i></button>
                                </div>

                                ):''} */}
                        {/* <button type="button" className="btn btn-secondary" onClick={this.UploadNow} >Enviar</button> */}
                    </div>
                </div>
                <div className="row">{headerFile}</div>

                <div className="row">
                    <div className="col-12">
                        <div className="text-center">
                            <button
                                type="button"
                                className="btn btn-success"
                                onClick={this.UploadNow}
                            >
                                Aceptar
                            </button>
                        </div>
                    </div>
                </div>
                {/* <div className="row mt-4 text-center">
                        <div className="col-12">
                            <div className="progress">
                                <div className="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style={{"width":"90%"}}>90%</div>
                            </div> 
                        </div>
                    </div> */}
            </div>
        );
    }
}

UploadFile.propTypes = {
    abreviatura: PropTypes.string,
    nombre_carpeta_destino: PropTypes.string,
    // carpeta: PropTypes.string,
    id_detalle_requerimiento: PropTypes.number,
    adjunto: PropTypes.array,
    getCantidadArchivos: PropTypes.func
};
export default UploadFile;
