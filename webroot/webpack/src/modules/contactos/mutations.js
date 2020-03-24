const setContacto = (state, contacto) => {
    state.contacto = contacto
}

const setContactos = (state, contactos) => {
    state.contactos = contactos
}

const isSaved = (state, isSaved) => {
    state.contactoGuardado = isSaved
}

export {
    setContacto,
    setContactos,
    isSaved,
}