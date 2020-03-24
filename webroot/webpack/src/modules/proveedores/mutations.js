const setProveedor = (state, proveedor) => {
    state.proveedor = proveedor
}

const setProveedores = (state, proveedores) => {
    state.proveedores = proveedores
}

const isSaved = (state, isSaved) => {
    state.proveedorGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.proveedorEliminado = isDeleted
}

export {
    setProveedor,
    setProveedores,
    isSaved,
    isDeleted,
}