var Vue = require('vue');

class Form{
    constructor(fields){
        this.originalData = fields;
        for(let field in fields){
            this[field] = fields[field];
        }

        this.status = 'ready';
        this.errors = new FormErrors;
        this.errorMessage = '';
    }

    data(){
        let data = Object.assign({}, this);
        delete data.errors;
        delete data.errorMessage;
        delete data.originalData;
        delete data.status;

        return data;
    }

    reset(){
        for(let field in this.originalData){
            this[field] = '';
        }
    }

    submit(url){
        this.status = 'loading';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#contact form input[name="_token"').val()
            }
        });
        $.ajax({
            type: "POST",
            url: url,
            data: this.data()
        }).done((data) => {
            this.reset();
            this.status = 'sent';
            setTimeout(() => this.status = 'ready', 10000);
        }).fail((xhr, status, error) => {
            if(xhr.status === 422){
                this.errors.record(xhr.responseJSON.errors);
                this.status = 'ready';
            }else{
                this.errorMessage = status+' '+xhr.status+': '+error;
                this.status = 'failed';
                setTimeout(() => this.status = 'ready', 10000);
            }
        });
    }
}

class FormErrors {
    constructor(){
        this.errors = {};
    }

    get(field){
        if(this.errors[field]){
            return this.errors[field][0]
        }
    }

    has(field){
        return this.errors.hasOwnProperty(field)
    }

    hasAny(){
        return Object.keys(this.errors).length > 0;
    }

    record(errors){
        this.errors = errors;
    }

    clear(field){
        delete this.errors[field]
    }
}

const app = new Vue({
    el: '#contact',

    data: {
        form: new Form({
            name: '',
            email: '',
            phone: '',
            message: '',
        })
    },

    methods: {
        errorClass(field){
            return this.form.errors.has(field) ? 'has-error' : false;
        },
        onSubmit(){
            this.form.submit('/contactRequest')
        }
    }
});