const setCliente = (state, cliente) => {
    state.cliente = cliente
}

const setClientes = (state, clientes) => {
    state.clientes = clientes
}

const isSaved = (state, isSaved) => {
    state.clienteGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.clienteEliminado = isDeleted
}

const setTipoClientes = (state, tipoClientes) => {
    state.tipoClientes = tipoClientes
}

export {
    setCliente,
    setClientes,
    isSaved,
    isDeleted,
    setTipoClientes,
}