const path                = require('path')
const LiveReloadPlugin    = require('webpack-livereload-plugin')
const webpack             = require('webpack')
const MomentLocalesPlugin = require('moment-locales-webpack-plugin')
const VueLoaderPlugin     = require('vue-loader/lib/plugin')

module.exports = {
    mode: 'development',
    watch: true,
    watchOptions: {
        aggregateTimeout: 300,
        poll: true
    },
    externals: {
        'jquery': 'jQuery',
    },
    plugins: [
        new LiveReloadPlugin({
            appendScriptTag: true
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            Vue: ['vue/dist/vue.esm.js', 'default'],
            _map: ['lodash', 'map'],
            moment: 'moment',
            Promise: 'es6-promise-promise',
        }),
        new webpack.DefinePlugin({
            PRODUCTION: JSON.stringify(false),
        }),
        new VueLoaderPlugin(),
    ],
    entry: {
        main     : '@babel/polyfill',
        dashboard: './src/scripts/Dashboard',

        /**
         * Clientes
         */
        clientes: './src/scripts/clientes/Agregar',

        /**
         * Usuarios
         */
        usuarios_login    : './src/scripts/usuarios/Login',
        usuarios_add      : './src/scripts/usuarios/Agregar',
        usuarios_timesheet: './src/scripts/usuarios/Timesheet',

        /**
         * Finanzas
         */
        gastos       : './src/scripts/finanzas/NuevoGasto',
        facturaciones: './src/scripts/finanzas/NuevaFacturacion',

        /**
         * Licencias
         */
        licencias: './src/scripts/licencias/Agregar',

        /**
         * Proveedores
         */
        proveedores: './src/scripts/proveedores/Agregar',

        /**
         * Proyectos
         */
        proyectos_add: './src/scripts/proyectos/Agregar',
        proyectos_editar: './src/scripts/proyectos/Editar',
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                exclude: /node_modules/,
                loader: 'ts-loader',
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: { loader: 'babel-loader' },
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
                // exclude: /node_modules/
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        ts: [
                            {
                                loader: 'ts-loader',
                                options: { appendTsSuffixTo: [/\.vue$/] }
                            }
                        ]
                    },
                    options: { esModule: true }
                }
            },
            {
                test: /\.(png|jpg|gif|svg|eot|ttf|woff|woff2)$/,
                loader: 'url-loader',
                options: {
                    limit: 10000
                }
            }
        ]
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js'],
        alias: {
            'vue': 'vue/dist/vue.common.js',
        },
    },
    output: {
        path: path.resolve(__dirname, '../dev'),
        filename: '[name].bundle.js',
        library: ['EntryPoint', '[name]'],
    },
};
