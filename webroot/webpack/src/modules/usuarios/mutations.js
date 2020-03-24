const setUsuario = (state, usuario) => {
    state.usuario = usuario
}

const setUsuarios = (state, usuarios) => {
    state.usuarios = usuarios
}

const isSaved = (state, isSaved) => {
    state.usuarioGuardado = isSaved
}

const isDeleted = (state, isDeleted) => {
    state.usuarioEliminado = isDeleted
}

export {
    setUsuario,
    setUsuarios,
    isSaved,
    isDeleted,
}