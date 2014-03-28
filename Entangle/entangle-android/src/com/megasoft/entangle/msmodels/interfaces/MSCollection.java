package com.megasoft.entangle.msmodels.interfaces;

import java.util.List;

import com.megasoft.entangle.msmodels.Promise;

public interface MSCollection<Model extends MSModel> {

	public Promise fetchAll();
	public List<Model> getModels();
	
}
