package com.megasoft.entangle;

import com.megasoft.requests.ImageRequest;
import android.support.v4.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class TransactionEntryFragment extends Fragment {
	
	private View view;

	/**
	 * Request id
	 */
	private int requestId;
	
	/**
	 * Requester name
	 */
	private String requester;
	
	/**
	 * Request description
	 */
	private String offerer;
	
	/**
	 * Transaction amount
	 */
	private int amount;
	
	/**
	 * The tangle Id
	 */
	private int tangleId;
	
	/**
	 * The requester Id
	 */
	private int requesterId;
	
	/**
	 * The ImageView that holds the user's profile picture
	 */
	private com.megasoft.entangle.views.RoundedImageView profilePictureView;

	/**
	 * The image url of the user's image
	 */
	private String imageURL;
	
	/**
	 * Sets the value of the requester id
	 * @param int requesterId
	 * @author Almgohar
	 */
	public void setRequesterId(int requesterId) {
		this.requesterId = requesterId;
	}
	
	/**
	 * Sets the value of the request id
	 * @param int requestId
	 * @author Almgohar
	 */
	public void setRequestId(int requestId) {
		this.requestId = requestId;
	}

	/**
	 * Sets the value of the requester name
	 * @param String requester
	 * @author Almgohar
	 */
	public void setRequester(String requester) {
		this.requester = requester;
	}

	/**
	 * Sets the value of the offerer name
	 * @param String offerer
	 * @author Almgohar
	 */
	public void setOfferer(String offerer) {
		this.offerer = offerer;
	}

	/**
	 * Sets the value of the transaction amount
	 * @param int amount
	 * @author Almgohar
	 */
	public void setAmount(int amount) {
		this.amount = amount;
	}
	
	/**
	 * Sets the value of the tangle Id
	 * @param int tangleId
	 * @author Almgohar
	 */
	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}
	
	/**
	 * Sets the value of the image url
	 * @param String url
	 * @author Almgohar
	 */
	public void setImageURL(String url) {
		this.imageURL = url;
	}
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState){
		this.view = inflater.inflate(R.layout.fragment_transaction_entry, container,false);
		((TextView)view.findViewById(R.id.offerer)).setText(offerer);
		((TextView)view.findViewById(R.id.transaction_amount)).setText(""+amount);
		((TextView)view.findViewById(R.id.requester)).setText(requester);
		profilePictureView = (com.megasoft.entangle.views.RoundedImageView) view.findViewById(R.id.requester_avatar);
		viewProfilePicture(imageURL);
		redirection();
		return view;
	}
	
	
	/**
	 * Views the user's profile picture
	 * @param String imageURL
	 * @author Almgohar
	 */ 
	public void viewProfilePicture(String imageURL) {
            ImageRequest image = new ImageRequest(profilePictureView);
            image.execute(imageURL);
	}
	
	/**
	 * Redirects to the offerer/requester profile
	 * @author Almgohar
	 */
	public void redirection() {
		((TextView)view.findViewById(R.id.transaction_description)).setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				goToRequest();				
			}
		});
		((TextView)view.findViewById(R.id.requester)).setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				goToProfile(requesterId);
			}
		});
		profilePictureView.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				goToProfile(requesterId);				
			}
		});
	}
	
	
	/**
	 * Redirects to a user's profile given his id
	 * @param int userId
	 * @author Almgohar
	 */
	private void goToProfile(int userId) {
		Intent profile = new Intent(getActivity().getBaseContext(), ProfileActivity.class);
		profile.putExtra("userId", userId);
		profile.putExtra("tangleId", tangleId);
		startActivity(profile);
	}
	
	/**
	 * Redirects to a request given the request id and tangle id
	 * @author Almgohar
	 */
	private void goToRequest() {
		Intent request = new Intent(getActivity().getBaseContext(), RequestActivity.class);
		request.putExtra("requestId", requestId);
		request.putExtra("tangleId", tangleId);
	}
}
