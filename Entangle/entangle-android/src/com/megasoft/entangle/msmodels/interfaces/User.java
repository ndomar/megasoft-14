package com.megasoft.entangle.msmodels.interfaces;

import com.megasoft.entangle.exceptions.InvalidPasswordException;

public interface User extends MSModel{

	public int getUserID();
	public void setUsername(String username);
	public String getUsername();
	public void setPassword() throws InvalidPasswordException;
	
	
}
