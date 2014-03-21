package com.megasoft.todo.interfaces.models;

import com.megasoft.todo.com.megasoft.todo.interfaces.Callback;

/**
 * Created by mohamedfarghal on 3/20/14.
 */
public interface Session {

    void authenticate(String username, String password, Callback callback);
    String getSessionId();
    String destroySession(String sessionId);

}
