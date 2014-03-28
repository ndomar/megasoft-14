package com.megasoft.entangle.msmodels.interfaces;

import com.megasoft.entangle.msmodels.Promise;

public interface MSModel {

	public static final String rootResource = "http://entangle2.apiary-mock.com";
	
	public Promise save();
	public Promise fetch();
}
