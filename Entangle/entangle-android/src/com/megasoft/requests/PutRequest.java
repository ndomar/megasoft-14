package com.megasoft.requests;

import java.io.UnsupportedEncodingException;

import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.json.JSONObject;

public class PutRequest extends HttpRequest {
	HttpPut httpPut;

	public PutRequest(String uri) {
		httpPut = new HttpPut(uri);
		super.setMethod(httpPut);
	}

	public void setBody(JSONObject body) {
		this.setHasBody(true);
		StringEntity data = null;
		try {
			data = new StringEntity(body.toString());
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
		this.httpPut.setEntity(data);
	}
}