package com.megasoft.entangle;

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
	 * The offerer Id
	 */
	private int offererId;

	
	/**
	 * Sets the value of the requester Id
	 * @param requesterId
	 */
	public void setRequesterId(int requesterId) {
		this.requesterId = requesterId;
	}

	/**
	 * Sets the value of the offerer Id
	 * @param offererId
	 */
	public void setOffererId(int offererId) {
		this.offererId = offererId;
	}

	/**
	 * Sets the value of the requester name
	 * @param requester
	 */
	public void setRequester(String requester) {
		this.requester = requester;
	}

	/**
	 * Sets the value of the request description
	 * @param request
	 */
	public void setOfferer(String offerer) {
		this.offerer = offerer;
	}

	/**
	 * Sets the value of the transaction amount
	 * @param amount
	 */
	public void setAmount(int amount) {
		this.amount = amount;
	}
	
	/**
	 * Sets the value of the tangle Id
	 * @param tangleId
	 */
	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState){
		this.view = inflater.inflate(R.layout.fragment_transaction_entry, container,false);
		((TextView)view.findViewById(R.id.offerer)).setText(offerer);
		((TextView)view.findViewById(R.id.transaction_amount)).setText(""+amount);
		((TextView)view.findViewById(R.id.requester)).setText(requester);
		redirection();
		return view;
	}
	
	/**
	 * Redirects to the offerer/requester profile
	 */
	public void redirection() {
		((TextView)view.findViewById(R.id.offerer)).setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				goToProfile(offererId);
			}
		});
		((TextView)view.findViewById(R.id.requester)).setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				goToProfile(requesterId);
			}
		});		
	}
	
	
	/**
	 * Redirects to a user's profile given his id
	 * 
	 * @param int userId
	 * @author Almgohar
	 */
	private void goToProfile(int userId) {
		Intent profile = new Intent(getActivity().getBaseContext(), ProfileActivity.class);
		profile.putExtra("userId", userId);
		profile.putExtra("tangleId", tangleId);
		startActivity(profile);
	}
	
}
