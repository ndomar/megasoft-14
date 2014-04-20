package com.megasoft.entangle;




import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

public class OfferActivity extends Activity {
	final Activity self = this;
	int offerId=1; 
	JSONObject returnedResponse =null;
	LinearLayout layout; 
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		acceptOffer();
		}
	
	
	public void acceptOffer() {
		layout = (LinearLayout) this.findViewById(R.id.offerLayout);
		GetRequest request = new GetRequest(
				"http://entangle2.apiary-mock.com/offer/" + offerId) {
			protected void onPostExecute(String response) {
				try {
					JSONObject json = new JSONObject(response);
					Log.e("test",response);
					JSONObject offerDetails =  (JSONObject) json.get("offerInformation");
					JSONObject requestDetails = (JSONObject) json.get("requestInformation"); 
				  	int requestId = Integer.parseInt(((String) requestDetails.get("requestID")));
				  	int requesterId = Integer.parseInt(((String) requestDetails.get("requesterID")));
				  	int requestStatus = Integer.parseInt(((String) requestDetails.get("requestStatus")));
					if (requestStatus != 0){
						return; 
					}
					else{
					
					int offerStatus = Integer.parseInt(((String) offerDetails.get("offerStatus")));
					int offererId = Integer.parseInt(((String) offerDetails.get("offererID")));
					if(offerStatus==0){
						returnedResponse = new JSONObject();
						returnedResponse.put("requestId", requestId);
						returnedResponse.put("offerId", offerId);
						returnedResponse.put("requesterId", requesterId);
						returnedResponse.put("offererId", offererId);	
						Button button = new Button(self);
						button.setText("Accept offer");
						layout.addView(button);
						OnClickListener l = null;
						button.setOnClickListener(l);	
					}
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}

//			private LinearLayout findViewById(Object layout) {
//				// TODO Auto-generated method stub
//				return null;
//			}
		};
		request.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
		request.execute();

//	}
}
}
