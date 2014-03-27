package com.megasoft.entangle.msmodels;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

import com.megasoft.entangle.msmodels.interfaces.DoneCallback;
import com.megasoft.entangle.msmodels.interfaces.FailCallback;
import com.megasoft.entangle.msmodels.interfaces.MSCollection;
import com.megasoft.entangle.msmodels.interfaces.MSModel;
import com.megasoft.requests.GetRequest;

public class RequestMSCollection implements MSCollection<RequestMSModel>{

	public void how_to_use_this() {
		final RequestMSCollection collection = new RequestMSCollection("/tangle/123/request", "oba7");
		collection.fetchAll().done(new DoneCallback() {
			
			@Override
			public void onDone(String response) {
				RequestMSModel model = collection.getModels().get(0);
				Log.e("walla3", model.getDescription());
				model.setDescription("allah allah");
				model.save();
				
			}
		}).fail(new FailCallback() {
			
			@Override
			public void onFail(Object response) {
				Log.e("walla3", response.toString());
				
			}
		});
	}
	
	private String resource;
	private String sessionId;
	private List<RequestMSModel> list;
	
	public RequestMSCollection(String resource, String sessionId) {
		this.resource = resource;
		this.sessionId = sessionId;
	}
	
	@Override
	public Promise fetchAll() {
		final Promise promise = new Promise();
		GetRequest request = new GetRequest(MSModel.rootResource + resource) {
			
				protected void onPostExecute(String res) {

					if (this.getStatusCode() >= 200 && this.getStatusCode() < 300){
						try {
							JSONObject json = new JSONObject(res);
							jsonToList(json.getJSONArray("requests"));
							promise.resolve("OK");
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}else { 
						promise.reject(this.getErrorMessage());
					}
				}
		};
		request.addHeader("X-SESSION-ID", sessionId);
		request.execute();
		return promise;
	}

	@Override
	public List<RequestMSModel> getModels() {
		return list;
	}
	
	private void jsonToList(JSONArray array) throws JSONException {
		list = new ArrayList<RequestMSModel>();
		for (int i = 0; i < array.length(); i++) {
			JSONObject json = array.getJSONObject(i);
			RequestMSModel model = new RequestMSModel(resource + "/" + json.getInt("requestId"), sessionId);
			model.setTitle(json.getString("title"));
			model.setId(json.getInt("requestId"));
			model.setDescription(json.getString("description"));
			model.setOffersCount(json.getInt("requestOffersCount"));
			JSONArray tags = new JSONArray(json.getString("tags"));
			for (int j = 0; j < tags.length(); j++) {
				model.addTag(tags.getString(i));
			}
			model.setRequestedPrice(json.getInt("requestedPrice"));	
			list.add(model);
		}
	}
}
