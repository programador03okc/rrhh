import React, { Component } from "react";


export class Redirection extends Component {
    constructor( props ){
        super();
        this.state = { ...props };


    }

    componentWillMount(){



        //window.location = this.state.route.loc;
        //window.location = 'https://meetflo.zendesk.com/hc/en-us/articles/230425728-Privacy-Policies';
        window.location = '/almacen';
    }
    render(){
       // return (<section>Redirecting...</section>);
        return (
            <div>
                <br />
                <span>Redirecting to {this.target}</span>
            </div>
        );
    }
}



export default Redirection;