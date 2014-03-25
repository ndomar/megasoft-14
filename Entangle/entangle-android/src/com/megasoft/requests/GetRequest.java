package com.megasoft.requests;

import org.apache.http.client.methods.HttpGet;

public class GetRequest extends HttpRequest {
	HttpGet httpGet;
	
	public GetRequest(String uri){
		httpGet = new HttpGet(uri);
		super.setMethod(httpGet);
	}
}