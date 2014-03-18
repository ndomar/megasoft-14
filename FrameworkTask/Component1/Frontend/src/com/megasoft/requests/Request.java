package com.megasoft.requests;


import android.os.AsyncTask;

public abstract class Request extends AsyncTask<String, String, String> {
	public boolean hasError = false;
	public String errorMessage = null;
	
	
	public boolean isHasError() {
		return hasError;
	}

	public String getErrorMessage() {
		return errorMessage;
	}
}
