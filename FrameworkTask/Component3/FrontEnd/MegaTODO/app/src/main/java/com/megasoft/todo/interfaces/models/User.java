package com.megasoft.todo.interfaces.models;

/**
 * Created by mohamedfarghal on 3/20/14.
 */
public interface User extends Model {

        void loadUser(String userId);
        void setUsername(String username);
        void setPassword(String password);

        String getUsername();


}
